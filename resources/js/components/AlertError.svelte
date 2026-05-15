<script lang="ts">
  import CircleAlert from 'lucide-svelte/icons/circle-alert';

  let {
    errors = [],
    title = 'Something went wrong.',
  }: {
    errors: string[];
    title?: string;
  } = $props();

  const uniqueErrors = $derived([...new Set(errors)]);
</script>

<div role="alert" class="du-alert du-alert-error">
  <CircleAlert class="size-4 shrink-0" />
  <div>
    <p class="font-semibold">{title}</p>
    <ul class="list-inside list-disc text-sm mt-1">
      {#each uniqueErrors as error (error)}
        <li>{error}</li>
      {/each}
    </ul>
  </div>
</div>
