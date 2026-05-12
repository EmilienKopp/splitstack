<script lang="ts">
  import InputError from './InputError.svelte';
  
  interface Props {
    checked?: boolean;
    label?: string;
    error?: string | null;
    errors?: string | string[] | null;
    class?: string;
    [key: string]: any
  }

  let { 
    checked = $bindable(false), 
    label,
    error,
    errors,
    class: className = '',
    ...rest 
  }: Props = $props();

  // Normalize error handling - support both 'error' and 'errors' props
  const normalizedError = $derived(
    error || 
    (typeof errors === 'string' ? errors : Array.isArray(errors) ? errors[0] : null)
  );
</script>

<fieldset class="fieldset {className}" data-error={normalizedError ? 'true' : 'false'}>
  <label class="label cursor-pointer justify-start gap-2">
    <input 
      type="checkbox" 
      class="checkbox" 
      bind:checked
      {...rest}
    />
    {#if label}
      <span class="label-text">{label}</span>
    {/if}
  </label>
  <InputError message={normalizedError} />
</fieldset>